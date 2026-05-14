import Foundation
import NativePHP

enum MobikulAppBarFunctions {
    private static let allowedIcons: Set<String> = ["back", "menu", "search", "cart", "profile", "wishlist", "share", "filter", "notifications", "more"]

    struct Configure: BridgeFunction {
        func run(parameters: [String: Any], resolve: ResolveBlock, reject: RejectBlock) {
            let title = (parameters["title"] as? String)?.trimmingCharacters(in: .whitespacesAndNewlines)
            let context = (parameters["context"] as? String)?.trimmingCharacters(in: .whitespacesAndNewlines)

            let finalTitle = (title?.isEmpty == false ? title! : "App")
            let finalContext = (context?.isEmpty == false ? context! : "default")
            let icons = MobikulAppBarFunctions.normalizeIcons(parameters["icons"], context: finalContext)
            let options = MobikulAppBarFunctions.normalizeOptions(parameters["options"])

            resolve([
                "title": finalTitle,
                "context": finalContext,
                "icons": icons,
                "options": options,
                "updated": true
            ])
        }
    }

    struct Reset: BridgeFunction {
        func run(parameters: [String: Any], resolve: ResolveBlock, reject: RejectBlock) {
            resolve([
                "title": "App",
                "context": "default",
                "icons": MobikulAppBarFunctions.defaultIcons(for: "default"),
                "options": MobikulAppBarFunctions.defaultOptions(),
                "updated": true
            ])
        }
    }

    private static func normalizeIcons(_ rawIcons: Any?, context: String) -> [[String: String]] {
        guard let rawList = rawIcons as? [[String: Any]] else {
            return defaultIcons(for: context)
        }

        let filtered = rawList.compactMap { item -> [String: String]? in
            guard let icon = (item["icon"] as? String)?.trimmingCharacters(in: .whitespacesAndNewlines),
                  let action = (item["action"] as? String)?.trimmingCharacters(in: .whitespacesAndNewlines),
                  !icon.isEmpty,
                  !action.isEmpty,
                  allowedIcons.contains(icon) else {
                return nil
            }

            return ["icon": icon, "action": action]
        }

        return filtered.isEmpty ? defaultIcons(for: context) : filtered
    }

    private static func defaultIcons(for context: String) -> [[String: String]] {
        switch context.lowercased() {
        case "home":
            return [["icon": "menu", "action": "open_menu"], ["icon": "search", "action": "open_search"], ["icon": "cart", "action": "open_cart"]]
        case "product":
            return [["icon": "back", "action": "go_back"], ["icon": "wishlist", "action": "add_to_wishlist"], ["icon": "share", "action": "share_product"]]
        case "checkout":
            return [["icon": "back", "action": "go_back"], ["icon": "cart", "action": "open_cart"]]
        case "profile":
            return [["icon": "back", "action": "go_back"], ["icon": "notifications", "action": "open_notifications"], ["icon": "more", "action": "open_more"]]
        default:
            return [["icon": "menu", "action": "open_menu"], ["icon": "search", "action": "open_search"]]
        }
    }

    private static func normalizeOptions(_ rawOptions: Any?) -> [String: Any] {
        let options = rawOptions as? [String: Any] ?? [:]
        let defaults = defaultOptions()

        return [
            "showAppLogo": booleanOption(options, key: "showAppLogo", fallback: defaults["showAppLogo"] as? Bool ?? false),
            "isElevated": booleanOption(options, key: "isElevated", fallback: defaults["isElevated"] as? Bool ?? true),
            "isLeadingEnable": booleanOption(options, key: "isLeadingEnable", fallback: defaults["isLeadingEnable"] as? Bool ?? false),
            "isHomeEnable": booleanOption(options, key: "isHomeEnable", fallback: defaults["isHomeEnable"] as? Bool ?? false),
            "isAppLogoForDarkmode": booleanOption(options, key: "isAppLogoForDarkmode", fallback: defaults["isAppLogoForDarkmode"] as? Bool ?? false),
            "appLogoUrl": stringOption(options, key: "appLogoUrl") ?? "",
            "darkAppLogoUrl": stringOption(options, key: "darkAppLogoUrl") ?? "",
            "placeHolderImage": stringOption(options, key: "placeHolderImage") ?? "",
            "appBarBackgroundColor": colorOption(options, key: "appBarBackgroundColor", fallback: defaults["appBarBackgroundColor"] as? String ?? "#ffffff"),
            "titleColor": colorOption(options, key: "titleColor", fallback: defaults["titleColor"] as? String ?? "#111827"),
            "titleFontSize": intOption(options, key: "titleFontSize", fallback: defaults["titleFontSize"] as? Int ?? 18, minimum: 12),
            "titleFontWeight": stringOption(options, key: "titleFontWeight") ?? "600",
            "logoWidth": intOption(options, key: "logoWidth", fallback: defaults["logoWidth"] as? Int ?? 32, minimum: 20),
            "logoHeight": intOption(options, key: "logoHeight", fallback: defaults["logoHeight"] as? Int ?? 32, minimum: 20)
        ]
    }

    private static func defaultOptions() -> [String: Any] {
        return [
            "showAppLogo": false,
            "isElevated": true,
            "isLeadingEnable": false,
            "isHomeEnable": false,
            "isAppLogoForDarkmode": false,
            "appLogoUrl": "",
            "darkAppLogoUrl": "",
            "placeHolderImage": "",
            "appBarBackgroundColor": "#ffffff",
            "titleColor": "#111827",
            "titleFontSize": 18,
            "titleFontWeight": "600",
            "logoWidth": 32,
            "logoHeight": 32
        ]
    }

    private static func booleanOption(_ options: [String: Any], key: String, fallback: Bool) -> Bool {
        return options[key] as? Bool ?? fallback
    }

    private static func stringOption(_ options: [String: Any], key: String) -> String? {
        guard let value = options[key] as? String else {
            return nil
        }

        let trimmed = value.trimmingCharacters(in: .whitespacesAndNewlines)
        return trimmed.isEmpty ? nil : trimmed
    }

    private static func intOption(_ options: [String: Any], key: String, fallback: Int, minimum: Int) -> Int {
        let value: Int

        switch options[key] {
        case let number as Int:
            value = number
        case let number as Double:
            value = Int(number)
        default:
            value = fallback
        }

        return max(minimum, value)
    }

    private static func colorOption(_ options: [String: Any], key: String, fallback: String) -> String {
        guard let value = stringOption(options, key: key) else {
            return fallback
        }

        let hexPattern = "^#[0-9a-fA-F]{3,8}$"
        let cssFunctionPattern = "^(rgb|rgba|hsl|hsla)\\([^)]+\\)$"

        if value.range(of: hexPattern, options: .regularExpression) != nil {
            return value
        }

        if value.range(of: cssFunctionPattern, options: .regularExpression) != nil {
            return value
        }

        return fallback
    }
}
